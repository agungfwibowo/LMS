import Quill from 'quill'
import 'quill/dist/quill.snow.css'
import Clipboard from '@ryangjchandler/alpine-clipboard'
import Intersect from '@alpinejs/intersect'
import './tools.js'

// Custom blot: <figure class="ql-align-*"><img src="..."><figcaption>...</figcaption></figure>
const BlockEmbed = Quill.import('blots/block/embed')

class FigureBlot extends BlockEmbed {
    static blotName = 'figure'
    static tagName = 'figure'

    static create(value) {
        const node = super.create()
        const img = document.createElement('img')
        img.setAttribute('src', value.src)
        node.appendChild(img)
        const cap = document.createElement('figcaption')
        cap.textContent = value.caption || ''
        node.appendChild(cap)
        if (value.align) {
            node.classList.add(`ql-align-${value.align}`)
        }
        return node
    }

    static value(node) {
        const alignClass = [...node.classList].find((c) => c.startsWith('ql-align-'))
        return {
            src: node.querySelector('img')?.getAttribute('src') ?? '',
            caption: node.querySelector('figcaption')?.textContent ?? '',
            align: alignClass ? alignClass.replace('ql-align-', '') : '',
        }
    }

    static formats(node) {
        const alignClass = [...node.classList].find((c) => c.startsWith('ql-align-'))
        return alignClass ? { align: alignClass.replace('ql-align-', '') } : {}
    }

    format(name, value) {
        if (name === 'align') {
            this.domNode.classList.remove('ql-align-center', 'ql-align-right', 'ql-align-justify')
            if (value) {
                this.domNode.classList.add(`ql-align-${value}`)
            }
        } else {
            super.format(name, value)
        }
    }
}

Quill.register(FigureBlot)

let revealObserver = null

function observeRevealElements() {
    if (!revealObserver) return
    document.querySelectorAll('[data-reveal]:not(.revealed)').forEach(el => revealObserver.observe(el))
}

document.addEventListener('alpine:initialized', () => {
    revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(({ target, isIntersecting }) => {
            if (isIntersecting) {
                target.classList.add('revealed')
                revealObserver.unobserve(target)
            }
        })
    }, { threshold: 0.12 })

    observeRevealElements()
})

document.addEventListener('livewire:navigated', observeRevealElements)
document.addEventListener('alpine:navigated', observeRevealElements)

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(Clipboard)
    window.Alpine.plugin(Intersect)

    window.Alpine.data('quillEditor', () => ({
        quill: null,

        init() {
            const self = this
            const initialContent = this.$el.dataset.content || ''
            const uploadUrl = this.$el.dataset.uploadUrl || ''

            this.quill = new Quill(this.$el, {
                theme: 'snow',
                placeholder: 'Tulis konten berita di sini...',
                modules: {
                    toolbar: [
                        [{ header: [2, 3, 4, 5, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['link', 'image'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'code-block'],
                        [{ align: [] }],
                        [{ color: [] }, { background: [] }],
                        ['clean'],
                    ],
                },
            })

            this.quill.getModule('toolbar').addHandler('image', () => {
                const input = document.createElement('input')
                input.type = 'file'
                input.accept = 'image/*'
                input.onchange = () => {
                    const file = input.files[0]
                    if (!file) return
                    const fd = new FormData()
                    fd.append('file', file)
                    fetch(uploadUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: fd,
                    })
                        .then((r) => r.json())
                        .then((d) => {
                            const count = self.quill.root.querySelectorAll('figure').length + 1
                            const range = self.quill.getSelection(true)
                            if (!range) return
                            self.quill.insertEmbed(range.index, 'figure', { src: d.url, caption: `Gambar ${count}` })
                            self.quill.setSelection(range.index + 1)
                        })
                }
                input.click()
            })

            if (initialContent) {
                this.quill.root.innerHTML = initialContent
            }

            // Expose for submit-time sync (see leaveGuard @submit handler)
            window._quillInstance = this.quill

            let initialized = false
            setTimeout(() => { initialized = true }, 0)

            this.quill.on('text-change', () => {
                self.$wire.content = self.quill.root.innerHTML
                if (initialized) {
                    self.$dispatch('quill-change')
                }
            })
        },

        destroy() {
            if (window._quillInstance === this.quill) {
                window._quillInstance = null
            }
        },
    }))

    window.Alpine.data('leaveGuard', () => ({
        isDirty: false,
        submitting: false,
        pendingUrl: null,
        showLeaveModal: false,

        init() {
            const self = this

            const onNavigated = () => {
                document.removeEventListener('alpine:navigate', onNavigate)
                window.removeEventListener('beforeunload', onBeforeUnload)
            }

            const onNavigate = (e) => {
                if (self.submitting || !self.isDirty) {
                    self.submitting = false
                    document.addEventListener('alpine:navigated', onNavigated, { once: true })
                    return
                }
                e.preventDefault()
                self.pendingUrl = e.detail.url.href
                self.showLeaveModal = true
            }

            const onBeforeUnload = (e) => {
                if (self.submitting || !self.isDirty) return
                e.preventDefault()
                e.returnValue = ''
            }

            document.addEventListener('alpine:navigate', onNavigate)
            window.addEventListener('beforeunload', onBeforeUnload)
        },

        confirmLeave() {
            const url = this.pendingUrl
            this.isDirty = false
            this.showLeaveModal = false
            this.$flux.modal('confirm-leave').close()
            window.Alpine.navigate(url)
        },
    }))

    // Guard for MODAL forms (add/edit). Prompts before discarding unsaved changes
    // when the modal is closed or the page is reloaded / navigated away from.
    //
    // Usage: x-data="formGuard({ prop: 'showForm', modal: 'category-form', confirm: 'confirm-leave' })"
    //   prop    - Livewire boolean property that controls the modal's open state
    //   modal   - Flux modal name of the form modal
    //   confirm - Flux modal name of the confirmation dialog (see <x-modal.confirm-leave>)
    window.Alpine.data('formGuard', (config = {}) => ({
        prop: config.prop || 'showForm',
        modalName: config.modal,
        confirmName: config.confirm || 'confirm-leave',
        isDirty: false,
        submitting: false,

        init() {
            const self = this

            // Fresh form population by Livewire can fire input events, so clear the
            // dirty flag on the next tick whenever the modal transitions to open.
            this.$wire.$watch(this.prop, (open) => {
                if (open) {
                    self.$nextTick(() => {
                        self.isDirty = false
                        self.submitting = false
                    })
                }
            })

            this._onBeforeUnload = (e) => {
                if (!self.isGuarding()) {
                    return
                }
                e.preventDefault()
                e.returnValue = ''
            }

            this._onNavigate = (e) => {
                if (!self.isGuarding()) {
                    return
                }
                e.preventDefault()
                self.attemptClose()
            }

            window.addEventListener('beforeunload', this._onBeforeUnload)
            document.addEventListener('alpine:navigate', this._onNavigate)

            // Route the modal's own Escape key and backdrop click through the guard so
            // they close freely when the form is clean, but prompt when it is dirty.
            // The Flux modal renders as a native <dialog data-modal="...">; its `cancel`
            // event (fired on Escape) is cancelable, and backdrop clicks land on the
            // dialog element itself. `:dismissible="false"` already disables Flux's own
            // backdrop close, so we own both interactions here.
            this._dialog = this.$el.querySelector(`dialog[data-modal="${this.modalName}"]`)

            if (this._dialog) {
                this._onCancel = (e) => {
                    e.preventDefault()
                    self.attemptClose()
                }

                this._onBackdropClick = (e) => {
                    if (e.target === self._dialog) {
                        self.attemptClose()
                    }
                }

                this._dialog.addEventListener('cancel', this._onCancel)
                this._dialog.addEventListener('click', this._onBackdropClick)
            }
        },

        destroy() {
            window.removeEventListener('beforeunload', this._onBeforeUnload)
            document.removeEventListener('alpine:navigate', this._onNavigate)

            if (this._dialog) {
                this._dialog.removeEventListener('cancel', this._onCancel)
                this._dialog.removeEventListener('click', this._onBackdropClick)
            }
        },

        isGuarding() {
            return this.$wire.get(this.prop) && this.isDirty && !this.submitting
        },

        markDirty() {
            if (!this.$wire.get(this.prop)) {
                return
            }
            this.isDirty = true
            this.submitting = false
        },

        onSubmit() {
            this.submitting = true
        },

        attemptClose() {
            if (!this.isDirty) {
                this.close()
                return
            }
            this.$flux.modal(this.confirmName).show()
        },

        close() {
            this.isDirty = false
            this.submitting = false
            this.$flux.modal(this.modalName).close()
        },

        confirmLeave() {
            this.$flux.modal(this.confirmName).close()
            this.close()
        },
    }))
})

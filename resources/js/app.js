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
                        [{ header: [1, 2, 3, false] }],
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
})

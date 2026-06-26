import Clipboard from '@ryangjchandler/alpine-clipboard'

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(Clipboard)
})
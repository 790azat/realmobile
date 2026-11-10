// Livewire подключает Alpine сам. Здесь только мелкие помощники.
window.copyText = async (text) => {
    try { await navigator.clipboard.writeText(text); return true; } catch (e) { return false; }
};

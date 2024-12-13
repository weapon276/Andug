function toggleLoading(button) {
    const originalContent = button.innerHTML;
    
    button.classList.add('loading');
    button.innerHTML = '<span></span>';
    
    setTimeout(() => {
        button.classList.remove('loading');
        button.innerHTML = originalContent;
    }, 1000);
}
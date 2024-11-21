document.querySelectorAll('.carousel img').forEach((img, index) => {
    img.style.display = index === 0 ? 'block' : 'none';
});

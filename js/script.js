document.addEventListener('DOMContentLoaded', () => {
    const burger = document.getElementById('burger-btn');
    const menu = document.getElementById('nav-menu');

    // Проверяем, что кнопка и меню существуют на странице
    if (burger && menu) {
        burger.addEventListener('click', () => {
            // Переключаем класс active (он прописан в твоем SCSS)
            menu.classList.toggle('active'); 
        });
    }
});
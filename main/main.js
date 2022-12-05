"use strict";

const loginPanel = document.querySelector('.auth');
const loginButton = document.querySelector('#login_open');
const loginClose = document.querySelector('#login_close');

const descriptionPanel = document.querySelector('.description');
const descriptionContent = descriptionPanel.querySelector('.description_content');
const descriptionClose = document.querySelector('#description_close');

while (objects.length > 0) {
    const element = objects.shift();
    const parentElement = document.getElementById(element.parent_id);

    if (parentElement) {
        const description = element.description ? element.description : '';

        parentElement.insertAdjacentHTML('beforeEnd', `
            <div class="object closed" id="${element.id}">
                <div class="object_header">
                    <span class="object_name">${element.name}</span>
                    <button
                        class="object_info"
                        data-description="${description}"
                    >i</button>
                    <button class="object_open">+</button>
                </div>
            </div>
        `);
    } else {
        objects.push(element);
    }
}

if (loginPanel) {
    loginButton.addEventListener('click', () => {
        loginPanel.classList.toggle('closed');
    });

    loginClose.addEventListener('click', () => {
        loginPanel.classList.add('closed');
    });
}

document.addEventListener('click', event => {
    const t = event.target;

    switch (t.className) {
        case 'object_info':
            descriptionPanel.classList.remove('closed');
            descriptionContent.textContent = t.dataset.description;
            break;
        case 'object_open':
            const parent = t.parentNode.parentNode;
            parent.classList.toggle('closed');
            t.textContent = t.textContent === '+' ? '-' : '+';
            break;
    }
});

descriptionClose.addEventListener('click', () => {
    descriptionPanel.classList.add('closed');
    descriptionContent.textContent = '';
});

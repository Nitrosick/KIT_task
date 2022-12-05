"use strict";

const infoLine = document.querySelector('.info');

const query = async (body) => {
    try {
        let response = await fetch('/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(body)
        });

        if (response.status >= 200 && response.status < 300) {
            return await response.json();
        }
    } catch (error) {
        return null;
    }
}

const changeObject = async (id, name, description, parent) => {
    const post = {
        action: 'change',
        id: id,
        name: name,
        description: description,
        parent_id: parent
    };

    let result;
    await query(post).then((data) => result = data);

    if (result && result.status === 'ok') {
        objectsRebuild();
        infoLine.textContent = 'Data changed';
    } else {
        infoLine.textContent = 'Error';
    }
}

const removeObject = async (id) => {
    const post = {
        action: 'remove',
        id: id
    };

    let result;
    await query(post).then((data) => result = data);

    if (result && result.status === 'ok') {
        objectsRebuild();
        infoLine.textContent = 'Object removed';
    } else {
        infoLine.textContent = 'Error';
    }
}

const addObject = async (name, description, parentId) => {
    const post = {
        action: 'add',
        name: name,
        description: description,
        parent_id: parentId
    };

    let result;
    await query(post).then((data) => result = data);

    if (result && result.status === 'ok') {
        objectsRebuild();
        infoLine.textContent = 'Object added';
    } else {
        infoLine.textContent = 'Error';
    }
}

const objectsRebuild = async () => {
    const post = {
        action: 'get_list'
    };

    let result;
    await query(post).then((data) => result = data);

    if (result && result.status === 'ok') {
        const objects = result.list;

        document.getElementById('1').innerHTML = `
            <div class="object_header">
                <input
                    class="object_name"
                    type="text"
                    value="Root"
                    data-id="1"
                >
            </div>
            <input
                class="object_description"
                type="text"
                value="Root description text"
                data-id="1"
            >
        `;

        while (objects.length > 0) {
            const element = objects.shift();
            const parentElement = document.getElementById(element.parent_id);

            if (parentElement) {
                const description = element.description ? element.description : '';

                parentElement.insertAdjacentHTML('beforeEnd', `
                    <div class="object" id="${element.id}">
                        <div class="object_header">
                            <input
                                class="object_name"
                                type="text"
                                value="${element.name}"
                                data-id="${element.id}"
                            >
                            <select
                                name="object_parent"
                                id="object_parent"
                                class="object_parent"
                                data-id="${element.id}"
                            >
                            </select>
                            <button
                                class="object_remove"
                                data-id="${element.id}"
                            >X</button>
                        </div>
                        <input
                            class="object_description"
                            type="text"
                            value="${description}"
                            data-id="${element.id}"
                        >
                    </div>
                `);
            } else {
                objects.push(element);
            }
        }

        parentsRebuild();
    }
};

const parentsRebuild = async () => {
    const post = {
        action: 'get_parents'
    };

    let result;
    await query(post).then((data) => result = data);

    if (result && result.status === 'ok') {
        let parentsList = '';

        result.list.forEach(el => {
            parentsList += `<option value="${el.id}">${el.id} | ${el.name}</option>`;
        });

        const addList = document.querySelector('.add_form_parent');
        const changeLists = document.querySelectorAll('.object_parent');

        addList.innerHTML = parentsList;

        changeLists.forEach(el => {
            el.innerHTML = '<option value="0">change parent</option>' + parentsList;
        });
    }
};

window.addEventListener('load', () => {
    objectsRebuild();
});

document.addEventListener('change', event => {
    const t = event.target;
    const element = document.getElementById(t.dataset.id);

    if (element) {
        const name = element.querySelector('.object_name').value;
        const description = element.querySelector('.object_description').value;
        const parent = element.querySelector('.object_parent').value;

        changeObject(t.dataset.id, name, description, parent);
    }
});

document.addEventListener('click', event => {
    const t = event.target;

    switch (t.className) {
        case 'object_remove':
            removeObject(t.dataset.id);
            break;
        case 'add_form_submit':
            event.preventDefault();

            const name = document.querySelector('.add_form_name').value;
            const description = document.querySelector('.add_form_desc').value;
            const parent = document.querySelector('.add_form_parent').value;

            if (!name || !description) {
                infoLine.textContent = 'Fill in the name and description';
            } else {
                addObject(name, description, parent);
            }
            break;
    }
});

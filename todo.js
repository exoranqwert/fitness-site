const todoList = [];
const baseTodoId = 'todoitem';

function deleteElement(id) {
    const index = todoList.findIndex(item => item.id === id);
    todoList.splice(index, 1);
    document.getElementById(baseTodoId + id).remove();
}

function formatDate(dateString) {
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('ru-RU', options);
}

function addToDo() {
    const form = document.forms.toDoForm;
    const newTodo = {
        id: createNewId(),
        title: form.elements.title.value,
        color: form.elements.color.value,
        description: form.elements.description.value,
        date: form.elements.date.value
    };
    
    if (!newTodo.title || !newTodo.date) {
        alert('Пожалуйста, заполните название и дату задачи');
        return;
    }
    
    todoList.push(newTodo);
    addToDoToHtml(newTodo);
    form.reset();
}

function createNewId() {
    return todoList.length === 0 ? 
        1 : Math.max(...todoList.map(todo => todo.id)) + 1;
}

function addToDoToHtml(newToDo) {
    const div = document.createElement('div');
    div.id = baseTodoId + newToDo.id;
    div.className = 'row my-3';
    
    const formattedDate = formatDate(newToDo.date);
    
    div.innerHTML = `
        <div class="col">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" 
                     style="height: 35px; background-color: ${newToDo.color}">
                    <span class="date-badge text-white">${formattedDate}</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">${newToDo.title}</h5>
                    <p class="card-text">${newToDo.description}</p>
                    <button type="button" class="btn btn-link text-danger"
                            onclick="deleteElement(${newToDo.id})">
                        Удалить задачу
                    </button>
                </div>
            </div>
        </div>`;
    
    document.getElementById('toDoContainer').append(div);
}
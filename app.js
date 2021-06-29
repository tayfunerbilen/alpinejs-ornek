import Alpine from 'alpinejs'
import request from './request'
window.Alpine = Alpine

// Alpine.data('search', () => ({
//     search: '',
//     items: ['tayfun erbilen', 'mehmet seven', 'gökhan kandemir'],
//     get filteredItems() {
//         return this.items.filter(
//             i => i.includes(this.search)
//         )
//     }
// }))

Alpine.data('counter', () => ({
    sayi: 0,
    decrementDisabled() {
        return this.sayi === 0 && 'disabled'
    },
    incrementDisabled() {
        return this.sayi === 10 && 'disabled'
    }
}))

Alpine.data('todolist', () => ({
    loading: true,
    todos: [],
    newTodo: '',
    async init() {
        const todos = await request('todos')
        this.todos = todos
        this.loading = false
    },
    async updateTodo(todo) {
        const done = todo.done === '1' ? '0' : '1'
        const result = await request('done-todo', {
            id: todo.id,
            done
        })
        if (result.done) {
            this.todos = this.todos.map(t => {
                if (t.id === todo.id) {
                    t.done = done
                }
                return t
            })
        }
    },
    async addTodo() {
        if (this.newTodo === '') {
            alert('Bir todo belirtin!')
            return
        }
        const result = await request('add-todo', {
            todo: this.newTodo
        })
        this.todos = [result, ...this.todos]
        this.newTodo = ''
    },
    async deleteTodo(todo) {
        const result = await request('delete-todo', {
            id: todo.id
        })
        if (result.deleted) {
            this.todos = this.todos.filter(t => t.id !== todo.id)
        }
    }
}))

Alpine.start()
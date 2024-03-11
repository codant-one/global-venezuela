import axios from '@axios'

class Migrants {

    get(params) {
        return axios.get('migrants', {params})
    }

    create(data) {
        return axios.post('/migrants', data)
    }

    show(id) {
        return axios.get(`/migrants/${id}`)
    }

    update(data) {
        return axios.post(`/migrants/${data.id}`, data.data)
    }

    delete(id){
        return axios.post(`/migrants/delete`, id)
    }
    
}

const migrants = new Migrants();

export default migrants;
import axios from '@axios'

class Volunteers {

    register(data) {
        return axios.post('volunteers/register', data)
    }

    get(params) {
        return axios.get('volunteers', {params})
    }

    create(data) {
        return axios.post('/volunteers', data)
    }

    show(id) {
        return axios.get(`/volunteers/${id}`)
    }

    update(data) {
        return axios.post(`/volunteers/${data.id}`, data.data)
    }

    delete(id){
        return axios.post(`/volunteers/delete`, id)
    }
}

const volunteers = new Volunteers();

export default volunteers;
import axios from '@axios'

class Inmigrants {

    get(params) {
        return axios.get('inmigrants', {params})
    }

    create(data) {
        return axios.post('/inmigrants', data)
    }

    show(id) {
        return axios.get(`/inmigrants/${id}`)
    }

    update(data) {
        return axios.post(`/inmigrants/${data.id}`, data.data)
    }

    delete(id){
        return axios.post(`/inmigrants/delete`, id)
    }
    
}

const inmigrants = new Inmigrants();

export default inmigrants;
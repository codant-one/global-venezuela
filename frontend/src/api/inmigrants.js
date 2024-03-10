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

    update(data, id) {
        return axios.put(`/inmigrants/${id}`, data)
    }

    delete(id){
        return axios.delete(`/inmigrants/${id}`)
    }
    
}

const inmigrants = new Inmigrants();

export default inmigrants;
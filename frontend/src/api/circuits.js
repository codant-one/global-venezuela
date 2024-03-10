import axios from '@axios'

class Circuits {

    get(params) {
        return axios.get('circuits', {params})
    }

    create(data) {
        return axios.post('/circuits', data)
    }

    show(params, id) {
        return axios.get(`/circuits/${id}`, {params})
    }

    update(data, id) {
        return axios.put(`/circuits/${id}`, data)
    }

    delete(id){
        return axios.delete(`/circuits/${id}`)
    }
    
}

const circuits = new Circuits();

export default circuits;
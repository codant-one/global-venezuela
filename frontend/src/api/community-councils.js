import axios from '@axios'

class CommunityCouncils {

    get(params) {
        return axios.get('community-councils', {params})
    }

    create(data) {
        return axios.post('/community-councils', data)
    }

    show(params, id) {
        return axios.get(`/community-councils/${id}`, {params})
    }

    update(data, id) {
        return axios.put(`/community-councils/${id}`, data)
    }

    delete(id){
        return axios.delete(`/community-councils/${id}`)
    }
    
}

const communityCouncils = new CommunityCouncils();

export default communityCouncils;
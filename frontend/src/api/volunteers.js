import axios from '@axios'

class Volunteers {

    register(data) {
        return axios.post('volunteers/register', data)
    }
}

const volunteers = new Volunteers();

export default volunteers;
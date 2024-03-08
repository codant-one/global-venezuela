import axios from '@axios'

class States {

    get() {
        return axios.get('states')
    }
}

const states = new States();

export default states;
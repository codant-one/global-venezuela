import axios from '@axios'

class Parishes {

    get() {
        return axios.get('parishes')
    }
}

const parishes = new Parishes();

export default parishes;
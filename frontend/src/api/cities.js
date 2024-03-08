import axios from '@axios'

class Cities {

    get() {
        return axios.get('cities')
    }
}

const cities = new Cities();

export default cities;
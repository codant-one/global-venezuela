import axios from '@axios'

class Municipalities {

    get() {
        return axios.get('municipalities')
    }
}

const municipalities = new Municipalities();

export default municipalities;
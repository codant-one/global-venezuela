import axios from '@axios'

class Miscellaneous {

    getData() {
        return axios.get('miscellaneous/data')
    }
}

const miscellaneous = new Miscellaneous();

export default miscellaneous;
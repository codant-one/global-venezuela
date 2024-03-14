import axios from '@axios'

class Miscellaneous {

    getData() {
        return axios.get('miscellaneous/data')
    }

    getDataMigrant() {
        return axios.get('miscellaneous/dataMigrant')
    }
}

const miscellaneous = new Miscellaneous();

export default miscellaneous;
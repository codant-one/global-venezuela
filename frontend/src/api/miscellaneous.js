import axios from '@axios'

class Miscellaneous {

    getData(params) {
        return axios.get('miscellaneous/data', {params})
    }

    getDataMigrant() {
        return axios.get('miscellaneous/dataMigrant')
    }
}

const miscellaneous = new Miscellaneous();

export default miscellaneous;
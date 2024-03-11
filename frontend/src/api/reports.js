import axios from '@axios'

class Reports {

    get(params) {
        return axios.get('reports', {params})
    }
}

const reports = new Reports();

export default reports;
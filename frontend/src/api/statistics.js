import axios from '@axios'

class Statistics {

    get() {
        return axios.get('dashboard-statistics')
    }
}

const statistics = new Statistics();

export default statistics;
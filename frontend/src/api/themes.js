import axios from '@axios'

class Themes {

    get() {
        return axios.get('themes')
    }
}

const themes = new Themes();

export default themes;
import { defineStore } from 'pinia'
import Themes from '@/api/themes'

export const useThemesStores = defineStore('themes', {
    state: () => ({
        themes: {},
        loading: false
    }),
    getters:{
        getThemes() {
          return this.themes
        }
    },
    actions: {
        setLoading(payload){
            this.loading = payload
        }, 
        fetchThemes(){
            this.setLoading(true)

            return Themes.get()
                .then((themes) => {
                    this.themes = themes.data.data
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                });
        }
    }
})

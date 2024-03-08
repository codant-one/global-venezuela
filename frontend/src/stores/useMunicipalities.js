import { defineStore } from 'pinia'
import Municipalities from '@/api/municipalities'

export const useMunicipalitiesStores = defineStore('municipalities', {
    state: () => ({
        municipalities: {},
        loading: false
    }),
    getters:{
        getMunicipalities() {
          return this.municipalities
        }
    },
    actions: {
        setLoading(payload){
            this.loading = payload
        }, 
        fetchMunicipalities(){
            this.setLoading(true)

            return Municipalities.get()
                .then((municipalities) => {
                    this.municipalities = municipalities.data.data
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                });
        }
    }
})

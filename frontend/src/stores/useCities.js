import { defineStore } from 'pinia'
import Cities from '@/api/cities'

export const useCitiesStores = defineStore('cities', {
    state: () => ({
        cities: {},
        loading: false
    }),
    getters:{
        getCities() {
          return this.cities
        }
    },
    actions: {
        setLoading(payload){
            this.loading = payload
        }, 
        fetchCities(){
            this.setLoading(true)

            return Cities.get()
                .then((cities) => {
                    this.cities = cities.data.data
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                });
        }
    }
})

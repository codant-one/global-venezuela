import { defineStore } from 'pinia'
import Parishes from '@/api/parishes'

export const useParishesStores = defineStore('parishes', {
    state: () => ({
        parishes: {},
        loading: false
    }),
    getters:{
        getParishes() {
          return this.parishes
        }
    },
    actions: {
        setLoading(payload){
            this.loading = payload
        }, 
        fetchParishes(){
            this.setLoading(true)

            return Parishes.get()
                .then((parishes) => {
                    this.parishes = parishes.data.data
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                });
        }
    }
})

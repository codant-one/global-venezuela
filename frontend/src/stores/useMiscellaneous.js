import { defineStore } from 'pinia'
import Miscellaneous from '@/api/miscellaneous'

export const useMiscellaneousStores = defineStore('miscellaneous', {
    state: () => ({
        data: {},
        loading: false
    }),
    getters:{
        getData() {
          return this.data
        }
    },
    actions: {
        setLoading(payload){
            this.loading = payload
        }, 
        fetchData(){
            this.setLoading(true)

            return Miscellaneous.getData()
                .then((miscellaneous) => {
                    this.data = miscellaneous.data.data
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                });
        }
    }
})
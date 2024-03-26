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
        fetchData(params){
            this.setLoading(true)

            return Miscellaneous.getData(params)
                .then((miscellaneous) => {
                    this.data = miscellaneous.data.data
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                });
        },
        fetchDataMigrant(){
            this.setLoading(true)

            return Miscellaneous.getDataMigrant()
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

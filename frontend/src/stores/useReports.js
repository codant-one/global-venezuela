import { defineStore } from 'pinia'
import Reports from '@/api/reports'

export const useReportsStores = defineStore('reports', {
    state: () => ({
        inmigrants: {},
        communityCouncils: {},
        loading: false,
        last_page: 1,
        inmigrantsTotalCount: 6,
    }),
    getters:{
        getInmigrants(){
            return this.inmigrants
        }
    },
    actions: {
        setLoading(payload){
            this.loading = payload
        },
        fetchInmigrants(params) {
            this.setLoading(true)
            
            return Reports.get(params)
                .then((response) => {
                    this.inmigrants = response.data.data.inmigrants.data
                    this.last_page = response.data.data.inmigrants.last_page
                    this.inmigrantsTotalCount = response.data.data.inmigrantsTotalCount
                })
                .catch(error => console.log(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        }
    }
})

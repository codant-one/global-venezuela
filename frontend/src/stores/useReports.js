import { defineStore } from 'pinia'
import Reports from '@/api/reports'

export const useReportsStores = defineStore('reports', {
    state: () => ({
        migrants: {},
        communityCouncils: {},
        loading: false,
        last_page: 1,
        migrantsTotalCount: 6,
    }),
    getters:{
        getMigrants(){
            return this.migrants
        }
    },
    actions: {
        setLoading(payload){
            this.loading = payload
        },
        fetchMigrants(params) {
            this.setLoading(true)
            
            return Reports.get(params)
                .then((response) => {
                    this.migrants = response.data.data.migrants.data
                    this.last_page = response.data.data.migrants.last_page
                    this.migrantsTotalCount = response.data.data.migrantsTotalCount
                })
                .catch(error => console.log(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        }
    }
})

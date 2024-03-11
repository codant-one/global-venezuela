import { defineStore } from 'pinia'
import Statistics from '@/api/statistics'

export const useStatisticsStores = defineStore('statistics', {
    state: () => ({
        statistics: {},
        loading: false
    }),
    getters:{
        getStatistics() {
          return this.statistics
        }
    },
    actions: {
        setLoading(payload){
            this.loading = payload
        }, 
        fetchStatistics(){
            this.setLoading(true)

            return Statistics.get()
                .then((statistics) => {
                    this.statistics = statistics.data
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                });
        }
    }
})
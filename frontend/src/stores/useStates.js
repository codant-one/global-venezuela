import { defineStore } from 'pinia'
import States from '@/api/states'

export const useStatesStores = defineStore('states', {
    state: () => ({
        states: {},
        loading: false
    }),
    getters:{
        getStates() {
          return this.states
        }
    },
    actions: {
        setLoading(payload){
            this.loading = payload
        }, 
        fetchStates(){
            this.setLoading(true)

            return States.get()
                .then((states) => {
                    this.states = states.data.data
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                });
        }
    }
})

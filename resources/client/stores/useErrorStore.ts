import { defineStore } from 'pinia'
import { ApiError } from '@/api/ApiError'

export const useErrorStore = defineStore('errorStore', {
  state: () => ({
    error: null as ApiError | Error | null
  }),
  actions: {
    setError(error: ApiError | Error) {
      this.error = error
    },
    clearError() {
      this.error = null
    }
  }
})

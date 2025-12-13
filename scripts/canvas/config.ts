/**
 * Canvas API configuration
 *
 * Set environment variables:
 * - CANVAS_BASE_URL: Canvas instance URL (default: https://canvas.docker)
 * - CANVAS_ACCESS_TOKEN: Canvas API access token (required)
 * - CANVAS_ACCOUNT_ID: Canvas account ID (default: 1)
 */

import { config as loadEnv } from 'dotenv'
import type { CanvasConfig } from './types.js'

// Load .env file from scripts/canvas directory
// Path is relative to project root where npm scripts are run
loadEnv({ path: 'scripts/canvas/.env' })

// Get environment variable or throw error if required
const getRequiredEnv = (key: string): string => {
  const value = process.env[key]
  if (!value) {
    throw new Error(`Missing required environment variable: ${key}`)
  }
  return value
}

// Get environment variable with default
const getEnv = (key: string, defaultValue: string): string =>
  process.env[key] || defaultValue

// Canvas API configuration
export const canvasConfig: CanvasConfig = {
  baseUrl: getEnv('CANVAS_BASE_URL', 'https://canvas.docker'),
  accessToken: getRequiredEnv('CANVAS_ACCESS_TOKEN'),
}

// Account ID to use for operations
export const accountId = parseInt(getEnv('CANVAS_ACCOUNT_ID', '1'), 10)

// Validate configuration
export const validateConfig = (): boolean => {
  try {
    if (!canvasConfig.accessToken) {
      console.error('Error: CANVAS_ACCESS_TOKEN environment variable is required')
      console.error('Make sure you have created scripts/canvas/.env with your Canvas access token')
      return false
    }

    // Log configuration for debugging (mask the token)
    const maskedToken = canvasConfig.accessToken.substring(0, 10) + '...'
    console.log('\n📋 Configuration:')
    console.log(`   Canvas URL: ${canvasConfig.baseUrl}`)
    console.log(`   Access Token: ${maskedToken}`)
    console.log(`   Account ID: ${accountId}\n`)

    return true
  } catch (error) {
    console.error('Configuration error:', error)
    return false
  }
}

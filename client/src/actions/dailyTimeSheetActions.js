import * as actionTypes from './dailyTimeSheetActionTypes'

export const editFlight = (flight) => {
  return {
    type: actionTypes.EDIT_FLIGHT,
    flight: flight
  }
}

export const finishEditFlight = () => {
  return {
    type: actionTypes.FINISH_EDIT_FLIGHT
  }
}
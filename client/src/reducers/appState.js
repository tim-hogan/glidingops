import * as actionTypes from '../actions/dailyTimeSheetActionTypes'

export default function appState(state = {}, action) {
  switch(action.type) {
    case actionTypes.EDIT_FLIGHT:
      const {flight} = action
      return {...state, editing: flight}
    case actionTypes.FINISH_EDIT_FLIGHT:
      return {...state, editing: null}
    default:
      return state
  }
}
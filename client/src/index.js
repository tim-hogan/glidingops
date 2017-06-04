import React from 'react'
import ReactDOM from 'react-dom'

import { createStore, applyMiddleware } from 'redux'
import thunk from 'redux-thunk'

import App from './containers/App'
import './index.css'

import FlightsSample     from './samples/FlightsSample'
import MembersSample     from './samples/MembersSample'
import LaunchTypesSample from './samples/LaunchTypesSample'
import AircraftsSample   from './samples/AircraftsSample'

import rootReducer from './reducers'

const preloadedState = {
  //TODO: rename appState to dailyTimeSheet
  appState: {
    flights:     FlightsSample.data,
    members:     MembersSample.data,
    launchTypes: LaunchTypesSample.data,
    aircrafts:   AircraftsSample.data,

    // state
    editing: null
  }
}
const enhancer = applyMiddleware(thunk)
const store = createStore(rootReducer, preloadedState, enhancer)

ReactDOM.render(
  <App store={store}/>,
  document.getElementById('root')
)

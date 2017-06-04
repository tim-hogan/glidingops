import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider'
import React, { Component } from 'react'
import PropTypes from 'prop-types'
import { Provider } from 'react-redux'

import {
  BrowserRouter as Router,
  Route
} from 'react-router-dom'

import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/css/bootstrap-theme.min.css'
import Main from './Main'
import './App.css'

// Before you do any rendering, initialize the plugin
import injectTapEventPlugin from 'react-tap-event-plugin'
injectTapEventPlugin()

const App = ({store}) => {
  return (
    <Provider store={store}>
      <MuiThemeProvider>
        <div className="container-fluid" style={{height: '100%'}}>
          <Router>
            <div style={{height: '100%'}}>
              <Route path='/' component={ Main } />
            </div>
          </Router>
        </div>
      </MuiThemeProvider>
    </Provider>
  )
}

App.propTypes = {
  store: PropTypes.object.isRequired
}

export default App

import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider'
import React, { Component } from 'react'

import {
  BrowserRouter as Router,
  Route
} from 'react-router-dom'

import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/css/bootstrap-theme.min.css'
import Main from './components/Main'
import './App.css'

// Before you do any rendering, initialize the plugin
import injectTapEventPlugin from 'react-tap-event-plugin';
injectTapEventPlugin();

class App extends Component {

  render() {
    return (
      <MuiThemeProvider>
        <div className="container-fluid" style={{height: '100%'}}>
          <Router>
            <div style={{height: '100%'}}>
              <Route path='/' component={ Main } />
            </div>
          </Router>
        </div>
      </MuiThemeProvider>
    )
  }
}

export default App

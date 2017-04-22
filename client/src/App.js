import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider'
import React, { Component } from 'react'

import {
  BrowserRouter as Router,
  Route
} from 'react-router-dom'

import Main from './components/Main'
import './App.css'

// Before you do any rendering, initialize the plugin
import injectTapEventPlugin from 'react-tap-event-plugin';
injectTapEventPlugin();

class App extends Component {

  render() {
    return (
      <MuiThemeProvider>
        <div className="App">
          <Router>
            <div>
              <Route path='/' component={ Main } />
            </div>
          </Router>
        </div>
      </MuiThemeProvider>
    )
  }
}

export default App

import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider'
import React, { Component } from 'react'
import AppBar from 'material-ui/AppBar'
import LeftNav from 'material-ui/LeftNav'
import logo from './logo.svg'
import './App.css'

// Before you do any rendering, initialize the plugin
import injectTapEventPlugin from 'react-tap-event-plugin';
injectTapEventPlugin();

class App extends Component {
  static defaultProps = {
    title: 'Dashboard'
  }

  toggleMenu = () => {
    this.setState({ menuOpen: !this.state.menuOpen })
  }

  render() {
    const menuItems = [
      { route: 'get-started', text: 'Get Started' },
      { route: 'customization', text: 'Customization' },
      { route: 'components', text: 'Components' },
      // { type: MenuItem.Types.SUBHEADER, text: 'Resources' },
      // {
      //    type: MenuItem.Types.LINK,
      //    payload: 'https://github.com/callemall/material-ui',
      //    text: 'GitHub'
      // },
      // {
      //    text: 'Disabled',
      //    disabled: true
      // },
      // {
      //    type: MenuItem.Types.LINK,
      //    payload: 'https://www.google.com',
      //    text: 'Disabled Link',
      //    disabled: true
      // },
    ]

    return (
      <MuiThemeProvider>
        <div className="App">
          <LeftNav
            open={this.state.menuOpen}
            docked={false}
            menuItems={menuItems} />
          <AppBar
            title={this.props.title}
            iconClassNameRight="muidocs-icon-navigation-expand-more"
            onLeftIconButtonTouchTap={this.toggleMenu}
          />
          <div className="App-header">
            <img src={logo} className="App-logo" alt="logo" />
            <h2>Welcome to React</h2>
          </div>
          <p className="App-intro">
            To get started, edit <code>src/App.js</code> and save to reload.
          </p>
        </div>
      </MuiThemeProvider>
    )
  }
}

export default App

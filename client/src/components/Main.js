import React, { Component } from 'react'
import { Route, NavLink as Link } from 'react-router-dom'

import AppBar from 'material-ui/AppBar'
import Drawer from 'material-ui/Drawer'
import { List, ListItem } from 'material-ui/List'
import ActionFlightTakeoff from 'material-ui/svg-icons/action/flight-takeoff'
import ActionToday from 'material-ui/svg-icons/action/today'

import PropTypes from 'prop-types'

import DailyTimeSheet from './DailyTimeSheet'
import Tracker        from './Tracker'

class Main extends Component {
  static propTypes = {
    children: PropTypes.node,
    title: PropTypes.string,
    match: PropTypes.object
  }

  static defaultProps = {
    title: 'Dashboard'
  }

  constructor(props) {
    super(props);
    this.state = {menuOpen: false};
  }

  toggleMenu = () => {
    this.setState(prevState => {
      return { menuOpen: !prevState.menuOpen }
    })
  }

  closeMenu = () => {
    this.setState({ menuOpen: false })
  }

  title = () => {
    return (
      <div>
      <Route exact path='/' render={ () => (<div>Daily time sheet</div>) } />
        <Route path='/daily-time-sheet' render={ () => (<div>Daily time sheet</div>) }/>
        <Route path='/tracker' render={ () => (<div>Tracker</div>) } />
      </div>
    )
  }

  render () {
    return (
      <div>
        <Drawer
          open={this.state.menuOpen}
          docked={false}
          onRequestChange={(menuOpen) => this.setState({menuOpen})}>
          <List>
            <ListItem primaryText='Daily time sheet'
                      leftIcon={<ActionToday />}
                      onTouchTap={this.closeMenu}
                      containerElement={ <Link to='/daily-time-sheet'/> } />
            <ListItem primaryText='Tracker'
                      leftIcon={<ActionFlightTakeoff />}
                      onTouchTap={this.closeMenu}
                      containerElement={ <Link to='/tracker'/> } />
          </List>
        </Drawer>
        <AppBar
          title={ this.title() }
          iconClassNameRight="muidocs-icon-navigation-expand-more"
          onLeftIconButtonTouchTap={this.toggleMenu}
        />

        <div>
          <Route exact path='/' component={ DailyTimeSheet } />
          <Route path='/daily-time-sheet' component={ DailyTimeSheet }/>
          <Route path='/tracker' component={ Tracker } />
        </div>
      </div>
    )
  }
}

export default Main
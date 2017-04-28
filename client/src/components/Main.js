import React, { Component } from 'react'
import PropTypes from 'prop-types'
import { Route, NavLink as Link } from 'react-router-dom'

import AppBar from 'material-ui/AppBar'
import Drawer from 'material-ui/Drawer'
import { List, ListItem } from 'material-ui/List'
import MapsFlight from 'material-ui/svg-icons/maps/flight'
import ActionToday from 'material-ui/svg-icons/action/today'


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
    this.state = {
      menuOpen: false,
    }
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

  renderDailyTimesheet = () => {
    return <DailyTimeSheet />
  }

  render () {
    return (
      <div style={{height: '100%'}}>
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
                      leftIcon={<MapsFlight />}
                      onTouchTap={this.closeMenu}
                      containerElement={ <Link to='/tracker'/> } />
          </List>
        </Drawer>
        <div className='row'>
          <div className='col-xs-12'>
            <AppBar
              title={ this.title() }
              iconClassNameRight="muidocs-icon-navigation-expand-more"
              onLeftIconButtonTouchTap={this.toggleMenu}
            />
          </div>
        </div>
        <div className='row'>
          <div className='col-xs-12'>
            <Route exact path='/' render={ this.renderDailyTimesheet }   />
            <Route path='/daily-time-sheet' render={ this.renderDailyTimesheet }/>
            <Route path='/tracker' component={ Tracker } />
          </div>
        </div>
      </div>
    )
  }
}

export default Main
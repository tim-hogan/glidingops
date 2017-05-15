import React, { Component } from 'react'
import PropTypes from 'prop-types'

import { NavLink as Link } from 'react-router-dom'

import AppBar from 'material-ui/AppBar'
import Drawer from 'material-ui/Drawer'
import { List, ListItem } from 'material-ui/List'

import MapsFlight from 'material-ui/svg-icons/maps/flight'
import ActionToday from 'material-ui/svg-icons/action/today'

class MainAppBar extends Component {
  static propTypes = {
    title: PropTypes.string
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

  render() {
    const title = (<div>{ this.props.title }</div>)
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
                      leftIcon={<MapsFlight />}
                      onTouchTap={this.closeMenu}
                      containerElement={ <Link to='/tracker'/> } />
          </List>
        </Drawer>
        <AppBar
          title={ title }
          onLeftIconButtonTouchTap={this.toggleMenu}
        />
      </div>
    )
  }
}

export default MainAppBar
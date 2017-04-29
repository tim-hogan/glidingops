import React, { Component } from 'react'
import PropTypes from 'prop-types'
import { Route } from 'react-router-dom'

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

  renderDailyTimesheet = () => {
    return <DailyTimeSheet />
  }

  render () {
    return (
      <div style={{height: '100%'}}>
        <div>
          <Route exact path='/' render={ this.renderDailyTimesheet }   />
          <Route path='/daily-time-sheet' render={ this.renderDailyTimesheet }/>
          <Route path='/tracker' component={ Tracker } />
        </div>
      </div>
    )
  }
}

export default Main
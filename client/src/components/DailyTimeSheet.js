import React, { Component } from 'react'
import PropTypes from 'prop-types'

import FlightEdit from './FlightEdit'
import EditAppBar from './EditAppBar'
import MainLayout from '../layouts/MainLayout'
import Flights    from './Flights'

// import './DailyTimeSheet.css'

class DailyTimeSheet extends Component {

  static propTypes = {
    appState: PropTypes.object.isRequired,
    actions:  PropTypes.object.isRequired,
    // editing : PropTypes.object.isRequired
  }

  constructor(props) {
    super(props)
  }

  render() {
    if(this.props.appState.editing) {
      const navigationComponent = <EditAppBar title={ `Edit flight ${this.props.appState.editing.seq}` }
                                        doneHandler={this.props.actions.finishEditFlight}/>
      return (
        <MainLayout navigationComponent={ navigationComponent }>
          <FlightEdit flight={this.props.appState.editing} />
        </MainLayout>
      )
    } else {
      return <Flights flights={this.props.appState.flights}
                      onEditFlight={this.props.actions.editFlight}/>
    }
  }
}

export default DailyTimeSheet
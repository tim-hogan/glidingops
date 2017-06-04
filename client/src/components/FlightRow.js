import React, { Component } from 'react'
import PropTypes from 'prop-types'

import IconButton from 'material-ui/IconButton'

import ActionFlightTakeoff from 'material-ui/svg-icons/action/flight-takeoff'
import ActionFlightLand from 'material-ui/svg-icons/action/flight-land'
import Create from 'material-ui/svg-icons/content/create'

import Moment from 'moment';

/**
 * Sample of expected flight JSON object
 * {
 *   "id": 1, "org": 1, "date": "2014-09-10 15:25:41", "localdate": 20140705, "updseq": 60, "location": "Paraparaumu", "seq": 1, "type": 1, "launchtype": 1, "towplane": 5, "glider": "GPJ", "towpilot": 48, "pic": 11, "p2": 5, "start": 1404514050639, "towland": 0, "land": 1404515944168, "height": 2500, "billing_option": 1, "billing_member1": null, "billing_member2": 5, "comments": "", "finalised": 1, "deleted": null,
 *   "relationships": {
 *      "pic": { "id": 11, ... },
 *      "p2":  { "id": 5, .... },
 *      .....
 *    }
 * }
 *
 */

class FlightRow extends Component {

  static propTypes = {
    flight: PropTypes.object,
    onEdit: PropTypes.func
  }

  launchtypeLabel = (flight) => {
    if(flight.relationships.launchtype.acronym === 'A') {
      return flight.relationships.towplane.rego_short
    }

    const launchtype = (flight.relationships.launchtype) ? flight.relationships.launchtype.name : ''
    return launchtype
  }

  formatDuration = (duration) => {
    const hours = duration.get('hours')
    const minutes = duration.get('minutes')
    let minutsStr = (minutes >= 10) ? `${minutes}` : `0${minutes}`

    return `${hours}:${minutsStr}`
  }

  editFlightHandler = (flight) => {
    return () => {
      console.log('Editing flight: ', flight)
      if(this.props.onEdit) {
        this.props.onEdit(flight)
      }
    }
  }

  takeOffHandler = (flight) => {
    return () => {
      console.log('Take off flight: ', flight)
    }
  }

  landHandler = (flight) => {
    return () => {
      console.log('Land flight: ', flight)
    }
  }

  takeOffField = (flight, start, land) => {
    return (
      (start) ? start.format('HH:mm') :
        <IconButton onTouchTap={this.takeOffHandler(flight)}
                        touch={true} tooltip="Take off" tooltipPosition="bottom-right">
          <ActionFlightTakeoff/>
        </IconButton>
    )
  }

  landField = (flight, start, land) => {
    if(!start) {
      return null
    }

    return (
      (land) ? land.format('HH:mm') :
        <IconButton onTouchTap={this.landHandler(flight)}
                        touch={true} tooltip="Take off" tooltipPosition="bottom-right">
          <ActionFlightLand/>
        </IconButton>
    )
  }

  render() {
    const { flight } = this.props
    const towpilotDisplayName = (flight.relationships.towpilot) ? flight.relationships.towpilot.displayname : ''
    const picDisplayName = (flight.relationships.pic) ? flight.relationships.pic.displayname : ''
    const p2DisplayName = (flight.relationships.p2) ? flight.relationships.p2.displayname : ''

    const start = (flight.start) ? Moment(flight.start) : null
    const land = (flight.land) ? Moment(flight.land) : null

    return (
      <tr>
        <td>
          <IconButton onTouchTap={this.editFlightHandler(flight)}
                      touch={true} tooltip="Edit" tooltipPosition="bottom-right">
            <Create/>
          </IconButton>
        </td>
        <td>{ flight.seq }</td>
        <td>{ this.launchtypeLabel(flight) }</td>
        <td>{ flight.glider }</td>
        <td>{ towpilotDisplayName }</td>
        <td>{ picDisplayName }</td>
        <td>{ p2DisplayName }</td>
        <td>{ this.takeOffField(flight, start, land) }</td>
        <td>{ this.landField(flight, start, land) }</td>
        <td>{ flight.height }</td>
        <td>{ (start && land) ? this.formatDuration(Moment.duration(land - start)) : null }</td>
        <td></td>
        <td style={{maxWidth: '100px'}}>{ flight.comments }</td>
        <td></td>
      </tr>
    )
  }
}

export default FlightRow
import React, { Component } from 'react'
import PropTypes from 'prop-types'

import ActionFlightTakeoff from 'material-ui/svg-icons/action/flight-takeoff'
import ActionFlightLand from 'material-ui/svg-icons/action/flight-land'
import Create from 'material-ui/svg-icons/content/create'

import Moment from 'moment';

import {
  TableRow, TableRowColumn
} from 'material-ui/Table'

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

class Flight extends Component {
  static propTypes = {
    flight: PropTypes.object
  }

  static mapStateToProps = (flightId, state) => {
    const flight =  state.flights.find( flight => {
      return flight.id === flightId
    })
    const launchType = state.launchTypes.find( el => {
      return el.id === flight.launchtype
    })
    const towpilot = state.members.find( el => {
      return el.id === flight.towpilot
    })
    const pic = state.members.find( el => {
      return el.id === flight.pic
    })
    const p2 = state.members.find( el => {
      return el.id === flight.p2
    })
    const towPlane = state.aircrafts.find( el => {
      return el.id === flight.towplane
    })

    return { ...flight, relationships: {
        launchtype: launchType,
        towpilot: towpilot,
        pic: pic,
        p2: p2,
        towplane: towPlane
      }
    }
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

  render() {
    const { flight } = this.props
    const towpilotDisplayName = (flight.relationships.towpilot) ? flight.relationships.towpilot.displayname : ''
    const picDisplayName = (flight.relationships.pic) ? flight.relationships.pic.displayname : ''
    const p2DisplayName = (flight.relationships.p2) ? flight.relationships.p2.displayname : ''

    const start = (flight.start) ? Moment(flight.start) : null
    const land = (flight.land) ? Moment(flight.land) : null
    const m = Moment

    return (
      <tr>
        <td><Create/></td>
        <td>{ flight.seq }</td>
        <td>{ this.launchtypeLabel(flight) }</td>
        <td>{ flight.glider }</td>
        <td>{ towpilotDisplayName }</td>
        <td>{ picDisplayName }</td>
        <td>{ p2DisplayName }</td>
        <td>{ (start) ? start.format('HH:mm') :  <ActionFlightTakeoff/> }</td>
        <td>{ (land) ? land.format('HH:mm') : <ActionFlightLand/> }</td>
        <td>{ flight.height }</td>
        <td>{ (start && land) ? this.formatDuration(Moment.duration(land - start)) : null }</td>
        <td></td>
        <td style={{maxWidth: '100px'}}>{ flight.comments }</td>
        <td></td>
      </tr>
    )
  }
}

export default Flight
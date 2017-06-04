import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'

import DailyTimeSheet from '../components/DailyTimeSheet'
import * as actions from '../actions/dailyTimeSheetActions'

const prepareFlights = (dailyTimeSheetData) => {
  const flightDetails = (flight) => {
    const flightId = flight.id
    const launchType = dailyTimeSheetData.launchTypes.find( el => {
      return el.id === flight.launchtype
    })
    const towpilot = dailyTimeSheetData.members.find( el => {
      return el.id === flight.towpilot
    })
    const pic = dailyTimeSheetData.members.find( el => {
      return el.id === flight.pic
    })
    const p2 = dailyTimeSheetData.members.find( el => {
      return el.id === flight.p2
    })
    const towPlane = dailyTimeSheetData.aircrafts.find( el => {
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

  const mappedFlights = dailyTimeSheetData.flights.map(flightDetails)
  return mappedFlights
}

const mapStateToProps = (state) => {
  const {appState} = state
  const flightsDetails = prepareFlights(appState)

  return {appState: {...appState, flights: flightsDetails} }
}

const mapDispatchToProps = (dispatch) => {
  return {
    actions: bindActionCreators(actions, dispatch)
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(DailyTimeSheet)
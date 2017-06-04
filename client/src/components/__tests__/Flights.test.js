import React    from 'react'

import { mount } from 'enzyme'

import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider'

import MainAppBar from '../MainAppBar'
import Flights    from '../Flights'

import injectTapEventPlugin from 'react-tap-event-plugin'
import {
  BrowserRouter as Router,
  Route
} from 'react-router-dom'

beforeAll(() => {
  injectTapEventPlugin()
})

it('renders without crashing', () => {
  const onEditFlight = jest.fn()
  const component = (
    <MuiThemeProvider>
      <Router>
        <Flights flights={[]} onEditFlight={onEditFlight}/>
      </Router>
    </MuiThemeProvider>
  )

  mount(component)
})
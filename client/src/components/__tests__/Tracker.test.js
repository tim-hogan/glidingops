import React    from 'react'
import PropTypes from 'prop-types'

import { mount } from 'enzyme'

import getMuiTheme from 'material-ui/styles/getMuiTheme'
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider'

import MainAppBar from '../MainAppBar'
import Tracker    from '../Tracker'

import injectTapEventPlugin from 'react-tap-event-plugin'
import {
  BrowserRouter as Router,
  Route
} from 'react-router-dom'

beforeAll(() => {
  injectTapEventPlugin()
})

const muiTheme = getMuiTheme()
const mountOptions = {
  context: {muiTheme},
  childContextTypes: {muiTheme: PropTypes.object}
}

it('renders without crashing', () => {
  const component = (
    <Router>
      <Tracker />
    </Router>
  )

  mount(component, mountOptions)
})

it('renders a main application bar', () => {
  const component = (
    <Router>
      <Tracker />
    </Router>
  )

  const wrapper = mount(component, mountOptions)

  const appBar = wrapper.find(MainAppBar).first()
  expect(appBar.exists()).toBeTruthy()
  expect(appBar.props().title).toBe('Flights live tracker')

  // expect(wrapper).toContainReact(<MainAppBar title={ 'Flights live tracker' }/>)
})
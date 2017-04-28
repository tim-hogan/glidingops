import React, { Component } from 'react'
import PropTypes from 'prop-types'

import TextField from 'material-ui/TextField'
import SelectField from 'material-ui/SelectField'
import MenuItem from 'material-ui/MenuItem'

const styles = {
  headline: {
    fontSize: 24,
    paddingTop: 16,
    marginBottom: 12,
    fontWeight: 400,
  },
}

class FlightEdit extends Component {
  static propTypes = {
    flight: PropTypes.object,
    members: PropTypes.object
  }

  constructor(props) {
    super(props)
  }

  onP2Select = () => {
    this.setState({})
  }

  render() {
    return (
      <div>
        <div className='row'>
          <div className='col-xs-12'>
            <h2 style={styles.headline}>#{this.props.flight.seq}</h2>
          </div>
        </div>
        <div className='row'>
          <div className='col-xs-4'>
            <TextField
              hintText="GLIDER rego"
              floatingLabelText="GLIDER"
              floatingLabelFixed={true} />
          </div>
        </div>
        <div className='row'>
          <div className='col-xs-4'>
            <SelectField
              floatingLabelText="LAUNCH"
              floatingLabelFixed={true}>

              <MenuItem value={1} primaryText="Never" />
              <MenuItem value={2} primaryText="Every Night" />
              <MenuItem value={3} primaryText="Weeknights" />
              <MenuItem value={4} primaryText="Weekends" />
              <MenuItem value={5} primaryText="Weekly" />

            </SelectField>
          </div>
          <div className='col-xs-4'>
            <TextField
              hintText="Club member"
              floatingLabelText="TOW PILOT WINCH DRIVER"
              floatingLabelFixed={true} />
          </div>
        </div>
        <div className='row'>
          <div className='col-xs-4'>
            <TextField
              hintText="Club member"
              floatingLabelText="PIC"
              floatingLabelFixed={true}
              readOnly={true} />
          </div>
          <div className='col-xs-4'>
            <TextField
              hintText="Club member"
              floatingLabelText="P2"
              floatingLabelFixed={true}
              readOnly={true} onTouchTap={() => {}} />
          </div>
          <div className='col-xs-4'>
            <TextField
              hintText="Club member"
              floatingLabelText="BILLING"
              floatingLabelFixed={true}
              readOnly={true} />
          </div>
        </div>
        <div className='row'>
          <div className='col-xs-12'>
            <TextField
              floatingLabelText="COMMENT"
              floatingLabelFixed={true}
              multiLine={true} />
          </div>
        </div>
      </div>
    )
  }
}

export default FlightEdit
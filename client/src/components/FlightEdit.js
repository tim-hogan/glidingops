import React, { Component } from 'react'
import PropTypes from 'prop-types'

import TextField from 'material-ui/TextField'
import SelectField from 'material-ui/SelectField'
import MenuItem from 'material-ui/MenuItem'

import MembersList    from './MembersList'

import MembersSample from '../samples/MembersSample'
import FlightsSample from '../samples/FlightsSample'

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
    this.state = {
      membersListOpen: false,
      onMemberSelected: () => {}
    }
  }

  onPicSelected = (member) => {
    console.log('PIC set to ', member.displayname)
    this.setState({membersListOpen: false})

    const targetflight = FlightsSample.data.find((flight) => {
      return flight.id === this.props.flight.id
    })
    targetflight.pic = member.id

    //TODO remove this once we use redux
    this.props.flight.pic = member.id
    this.props.flight.relationships.pic = member
  }

  onP2Selected = (member) => {
    console.log('P2 set to ', member.displayname)
    this.setState({membersListOpen: false})

    const targetflight = FlightsSample.data.find((flight) => {
      return flight.id === this.props.flight.id
    })
    targetflight.p2 = member.id

    //TODO remove this once we use redux
    this.props.flight.p2 = member.id
    this.props.flight.relationships.p2 = member
  }

  picTouchHandler = (event) => {
    this.setState({
      membersListOpen: true,
      onMemberSelected: this.onPicSelected
    })
  }

  p2TouchHandler = (event) => {
    this.setState({
      membersListOpen: true,
      onMemberSelected: this.onP2Selected
    })
  }

  render() {
    const picDisplayName = (this.props.flight.relationships.pic) ? this.props.flight.relationships.pic.displayname : ''
    const p2DisplayName = (this.props.flight.relationships.p2) ? this.props.flight.relationships.p2.displayname : ''

    return (
      <div>
        <MembersList
          open={this.state.membersListOpen}
          onSelect={this.state.onMemberSelected}
          onRequestClose={() => {this.setState({membersListOpen: false})}}
          members={MembersSample.data}/>
        <div className='row'>
          <div className='col-xs-12'>
            <h2 style={styles.headline}>#{this.props.flight.seq}</h2>
          </div>
        </div>
        <div className='row'>
          <div className='col-sm-4'>
            <TextField style={{width: '100%'}}
              hintText="GLIDER rego"
              floatingLabelText="GLIDER"
              floatingLabelFixed={true} />
          </div>
        </div>
        <div className='row'>
          <div className='col-sm-4'>
            <SelectField style={{width: '100%'}}
              floatingLabelText="LAUNCH"
              floatingLabelFixed={true}>

              <MenuItem value={1} primaryText="Never" />
              <MenuItem value={2} primaryText="Every Night" />
              <MenuItem value={3} primaryText="Weeknights" />
              <MenuItem value={4} primaryText="Weekends" />
              <MenuItem value={5} primaryText="Weekly" />

            </SelectField>
          </div>
          <div className='col-sm-4'>
            <TextField style={{width: '100%'}}
              hintText="Club member"
              floatingLabelText="TOW PILOT WINCH DRIVER"
              floatingLabelFixed={true} />
          </div>
        </div>
        <div className='row'>
          <div className='col-sm-4'>
            <TextField style={{width: '100%'}}
              hintText="Club member"
              floatingLabelText="PIC"
              floatingLabelFixed={true}
              value={picDisplayName}
              readOnly={true} onTouchTap={this.picTouchHandler}/>
          </div>
          <div className='col-sm-4'>
            <TextField style={{width: '100%'}}
              hintText="Club member"
              floatingLabelText="P2"
              floatingLabelFixed={true}
              value={p2DisplayName}
              readOnly={true} onTouchTap={this.p2TouchHandler} />
          </div>
          <div className='col-sm-4'>
            <TextField style={{width: '100%'}}
              hintText="Club member"
              floatingLabelText="BILLING"
              floatingLabelFixed={true}
              readOnly={true} />
          </div>
        </div>
        <div className='row'>
          <div className='col-xs-12'>
            <TextField style={{width: '100%'}}
              floatingLabelText="COMMENT"
              floatingLabelFixed={true}
              value={this.props.flight.comments}
              multiLine={true} />
          </div>
        </div>
      </div>
    )
  }
}

export default FlightEdit
import * as React from 'react';
import { Login } from '../../components/user/login';
import { PasswordReset } from '../../components/user/pwd-reset';
import { Register } from '../../components/user/register';
import { NavBar } from '../../components/common/navbar';
import { MobileRouteProps } from '../router';
import { Page } from '../../components/common/page';


interface State {
}

export class LoginRoute extends React.Component<MobileRouteProps, State> {
  public location = '';

  public render () {
    const content = this.renderContent();

    return <Page top={<NavBar goBack={this.props.core.history.goBack}>Login</NavBar>} >
      { content }
    </Page>;
  }

  public renderContent () {
    switch (window.location.pathname) {
      case '/login':
        this.location = 'login';
        return <Login login={async (email, pwd) => 
          this.props.location.state && 
          this.props.location.state.from?
            await this.props.core.db.login(
              email,
              pwd,
              this.props.location.state.from) : 
              await this.props.core.db.login(email, pwd) }></Login>;
      case '/register':
        this.location = 'register';
        return <Register register={async (name, email, pwd) => await this.props.core.db.register({
          email,
          name,
          password: pwd,
        })}></Register>;
      case '/reset_password':
        this.location = 'reset password';
        return <PasswordReset resetPassword={(email) => this.props.core.db.resetPassword(email)}></PasswordReset>;
      default:
        return <div>wrong pathname {window.location.pathname}</div>;
    }
  }
}
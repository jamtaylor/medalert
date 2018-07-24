
def ideal_gas_law(P, V, n, R, T):
    if P == "?":
        return (n*R*T)/V
    elif V == "?":
        return (n*R*T)/P
    elif n == "?":
        return (P*V)/(R*T)
    elif R == "?":
        return (P*V)/(n*T)
